<?php

namespace Ketcau\Repository;

use Doctrine\Persistence\ManagerRegistry as RegistryInterface;
use Ketcau\Entity\BlockPosition;

class BlockPositionRepository extends AbstractRepository
{
    protected $blockRepository;


    public function __construct(BlockRepository $blockRepository, RegistryInterface $registry)
    {
        parent::__construct($registry, BlockPosition::class);
        $this->blockRepository = $blockRepository;
    }


    public function register($data, $Blocks, $UnusedBlocks, $Layout): void
    {
        $em = $this->getEntityManager();

        $max = count($Blocks) + count($UnusedBlocks);
        for ($i = 0; $i < $max; $i++) {
            if (!isset($data['block_id_'. $i])) {
                continue;
            }
            if ($data['section_'. $i] == \Ketcau\Entity\Layout::TARGET_ID_UNUSED) {
                continue;
            }
            $Block = $this->blockRepository->find($data['block_id_'. $i]);
            $BlockPosition = new BlockPosition();
            $BlockPosition
                ->setBlockId($data['block_id_'. $i])
                ->setLayoutId($Layout->getId())
                ->setBlockRow($data['block_row_'. $i])
                ->setSection($data['section_'. $i])
                ->setBlock($Block)
                ->setLayout($Layout);
            $Layout->addBlockPosition($BlockPosition);
            $em->persist($BlockPosition);
            $em->flush();
        }
    }
}