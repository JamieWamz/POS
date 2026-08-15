-- Allow administrator-supplied HTTPS CDN URLs for branded product imagery.
ALTER TABLE products
  MODIFY image VARCHAR(2048) NOT NULL DEFAULT 'views/img/products/default/anonymous.png';
