FROM node:20 AS frontend

WORKDIR /app

COPY package*.json ./
RUN npm install

COPY . .
RUN npm run build


FROM nginx:1.27-alpine

WORKDIR /var/www

COPY public /var/www/public
COPY --from=frontend /app/public/build /var/www/public/build
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

RUN mkdir -p /var/www/storage/app/public \
    && ln -sf /var/www/storage/app/public /var/www/public/storage

EXPOSE 80
