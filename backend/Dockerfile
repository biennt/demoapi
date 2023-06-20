FROM node:alpine
RUN mkdir /app
COPY * /app
WORKDIR /app
RUN npm install
CMD ["node", "app"]
EXPOSE 3000

