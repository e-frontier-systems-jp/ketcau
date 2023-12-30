FROM node:18.19.0-slim

RUN apt update -y && apt upgrade -y
RUN apt-get install -y curl wget

WORKDIR /home/app

ENV TINI_VERSION v0.19.0
ADD https://github.com/krallin/tini/releases/download/${TINI_VERSION}/tini-static /tini
RUN chmod +x /tini

USER node
ENV PORT 3000

EXPOSE 3000

ENTRYPOINT ["/tini", "--", "npx"]
CMD ["nuxt", "dev"]
