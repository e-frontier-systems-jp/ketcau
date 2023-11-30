<?php

namespace Ketcau\Service;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManagerInterface;
use Ketcau\Annotation\EntityExtension;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;

class EntityProxyService
{
    protected $entityManager;

    protected $container;


    public function __construct(
        EntityManagerInterface $entityManager,
        ContainerInterface $container
    )
    {
        $this->entityManager = $entityManager;
        $this->container = $container;
    }


    public function generate($includeDirs, $excludeDirs, $outputDir, OutputInterface $output = null)
    {
        if (is_Null($output)) {
            $output = new ConsoleOutput();
        }

        $generatedFiles = [];

        list($addTraits, $removeTraits) = $this->scanTraits($includeDirs, $excludeDirs);
        $targetEntities = array_unique(array_merge(array_keys($addTraits), array_keys($removeTraits)));

        foreach ($targetEntities as $targetEntity) {
            $traits = isset($addTraits[$targetEntity]) ? $addTraits[$targetEntity] : [];
            $fileName = $this->originalEntityPath($targetEntity);
            $baseName = basename($fileName);
            $entityTokens = Tokens::fromCode(file_get_contents($fileName));

            if (strpos($fileName, 'app/proxy/entity') === false) {
                $this->removeClassExistsBlock($entityTokens);
            } else {
                $fileName = str_replace('/app/proxy/entity', '', $fileName);
            }

            if (isset($removeTraits[$targetEntity])) {
                foreach ($removeTraits[$targetEntity] as $trait) {
                    $this->removeTrait($entityTokens, $trait);
                }
            }

            foreach ($traits as $trait) {
                $this->addTrait($entityTokens, $trait);
            }
            $projectDir = str_replace('\\', '/', $this->container->getParameter('kernel.project_dir'));

            $baseDir = str_replace($projectDir, '', str_replace($baseName, '', $fileName));
            if (!file_exists($outputDir.$baseDir)) {
                mkdir($outputDir.$baseDir, 0777, true);
            }

            $file = ltrim(str_replace($projectDir, '', $fileName), '/');
            $code = $entityTokens->generateCode();
            $generatedFiles[] = $outputFile = $outputDir.'/'.$file;

            file_put_contents($outputFile, $code);
            $output->writeln('gen -> '.$outputFile);
        }

        return $generatedFiles;
    }


    private function originalEntityPath(string $entityClassName): string
    {
        $projectDir = rtrim(str_replace('\\', '/', $this->container->getParameter('kernel.project_dir')), '/');
        $originalPath = null;

        if (preg_match('/\AKetcau\\\\Entity\\\\(.+)\z/', $entityClassName, $matches)) {
            $pathToEntity = str_replace('\\', '/', $matches[1]);
            $originalPath = sprintf('%s/src/Ketcau/Entity/%s.php', $projectDir, $pathToEntity);
        }
        if (preg_match('/\ACustomize\\\\Entity\\\\(.+)\z/', $entityClassName, $matches)) {
            $pathToEntity = str_replace('\\', '/', $matches[1]);
            $originalPath = sprintf('%s/app/Customize/Entity/%s.php', $projectDir, $pathToEntity);
        }
        if (preg_match('/\APlugin\\\\([^\\\\]+)\\\\Entity\\\\(.+)\z/', $entityClassName, $matches)) {
            $pathToEntity = str_replace('\\', '/', $matches[2]);
            $originalPath = sprintf('%s/app/Plugin/%s/Entity/%s.php', $projectDir, $matches[1], $pathToEntity);
        }

        if ($originalPath !== null && file_exists($originalPath)) {
            return $originalPath;
        }

        $rc = new \ReflectionClass($entityClassName);
        return str_replace('\\', '/', $rc->getFileName());
    }


    protected function scanTraits($dirSets)
    {
        $includedFileSets = [];
        foreach ($dirSets as $dirSet) {
            $includedFiles = [];
            $dirs = array_filter($dirSet, 'file_exists');
            if (!empty($dirs)) {
                $files = Finder::create()
                    ->in($dirs)
                    ->name('*.php')
                    ->files();

                foreach ($files as $file) {
                    require_once $file->getRealPath();
                    $includedFiles = $file->getRealPath();
                }
            }
            $includedFileSets[] = $includedFiles;
        }

        $declaredTraits = array_map(function ($fqcn) {
            return strpos($fqcn, '\\') === 0 ? $fqcn : '\\'. $fqcn;
        }, get_declared_traits());

        $traitSets = array_map(function() { return []; }, $dirSets);
        foreach ($declaredTraits as $className) {
            $rc = new \ReflectionClass($className);
            $sourceFile = $rc->getFileName();
            foreach ($includedFileSets as $index => $includedFiles) {
                if (in_array($sourceFile, $includedFiles)) {
                    $traitSets[$index][] = $className;
                }
            }
        }

        $reader = new AnnotationReader();
        $proxySets = [];
        foreach ($traitSets as $traits) {
            $proxies = [];
            foreach ($traits as $trait) {
                $anno = $reader->getClassAnnotation(new \ReflectionClass($trait), EntityExtension::class);
                if ($anno) {
                    $class = str_replace('\\\\', '\\', $anno->value());
                    $class = ltrim($class, '\\');
                    $proxies[$class][] = $trait;
                }
            }
            $proxySets[] = $proxies;
        }

        return $proxySets;
    }



    private function addTrait($entityTokens, $trait)
    {
        $newTraitTokens = $this->convertTraitNameToTokens($trait);

        $useTraitIndex = $entityTokens->getNextTokenOfKind(0, [[CT::T_USE_TRAIT]]);

        if ($useTraitIndex > 0) {
            $useTraitEndIndex = $entityTokens->getNextTokenOfKind($useTraitIndex, [';']);
            $alreadyUseTrait = $entityTokens->findSequence($newTraitTokens, $useTraitIndex, $useTraitEndIndex);
            if (is_null($alreadyUseTrait)) {
                $entityTokens->insertAt($useTraitEndIndex, array_merge(
                    [new Token(','), new Token([T_WHITESPACE, ' '])],
                    $newTraitTokens
                ));
            }
        } else
        {
            $useTraitTokens = array_merge(
                [
                    new Token([T_WHITESPACE, PHP_EOL. '    ']),
                    new Token([CT::T_USE_TRAIT, 'use']),
                    new Token([T_WHITESPACE, ' ']),
                ],
                $newTraitTokens,
                [   new Token(';'),
                    new Token([T_WHITESPACE, PHP_EOL])
                ],
            );

            $classTokens = $entityTokens->findSequence([[T_CLASS], [T_STRING]]);
            $classTokenEnd = $entityTokens->getNextTokenOfKind(array_keys($classTokens)[0], ['{']);
            $entityTokens->insertAt($classTokenEnd + 1, $useTraitTokens);
        }
    }


    private function removeTrait($entityTokens, $trait)
    {
        $useTraitIndex = $entityTokens->getNextTokenOfKind(0, [[CT::T_USE_TRAIT]]);
        if ($useTraitIndex > 0) {
            $useTraitEndIndex = $entityTokens->getNextTokenOfKind($useTraitIndex, [';']);
            $traitsTokens = array_slice($entityTokens->toArray(), $useTraitIndex + 1, $useTraitEndIndex - $useTraitIndex - 1);

            $traitNames = explode(',', implode(array_map(function ($token) {
                return $token->getContent();
            }, array_filter($traitsTokens, function ($token) {
                return $token->getId() != T_WHITESPACE;
            }))));

            foreach ($traitNames as $i => $name) {
                if ($name === $trait) {
                    unset($traitNames[$i]);
                }
            }

            $entityTokens->clearRange($useTraitIndex, $useTraitEndIndex + 1);

            foreach ($traitNames as $t) {
                $this->addTrait($entityTokens, $t);
            }
        }
    }


    private function convertTraitNameToTokens($name)
    {
        $result = [];
        $i = 0;

        foreach (explode('\\', $name) as $part) {

            if ($part) {
                if ($i > 0) {
                    $result[] = new Token([T_NS_SEPARATOR], '\\');
                }
                $result[] = new Token([T_STRING], $part);
            }

            $i++;
        }

        return $result;
    }


    private function removeClassExistsBlock(Tokens $entityTokens)
    {
        $startIndex = $entityTokens->getNextTokenOfKind(0, [[T_IF]]);
        $classIndex = $entityTokens->getNextTokenOfKind(0, [[T_CLASS]]);

        if ($startIndex > 0 && $startIndex < $classIndex) {
            $blockStartIndex = $entityTokens->getNextTokenOfKind($startIndex, ['{']);
            $blockEndIndex = $entityTokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $blockStartIndex);

            $entityTokens->clearRange($startIndex, $blockStartIndex);
            $entityTokens->clearRange($blockEndIndex, $blockEndIndex + 1);
        }
    }
}