# PHP PDO E-Commerce (OOP + XAMPP)

This project now follows an organized OOP structure while keeping the same stack:

- HTML
- CSS
- JavaScript
- PHP
- XAMPP (Apache + MySQL)
- PDO

## Project structure

- [app/Core](app/Core): Config, Database, Session core classes
- [app/Models](app/Models): Product, User, Order models
- [app/Services](app/Services): AuthService, CartService business logic
- [app/Support](app/Support): global helper functions
- [app/Views/partials](app/Views/partials): shared header/footer
- [bootstrap/app.php](bootstrap/app.php): application bootstrap + autoload
- [public](public): front controller pages (shop, auth, cart, checkout)
- [assets](assets): CSS and JavaScript frontend assets
- [sql/database.sql](sql/database.sql): database schema + seed

## Features

- Product catalog + product details
- Authentication: sign up, login, logout
- Session cart: add/update/remove/clear
- Checkout with authenticated user
- Orders + order items persisted with PDO transaction
- Stock update after successful order

## Configure and run

1. Put this folder under XAMPP htdocs so it is available at `Web1/E-commerce`.
2. Start Apache and MySQL in XAMPP.
3. Open phpMyAdmin and import [sql/database.sql](sql/database.sql).
4. Open [app/Core/Config.php](app/Core/Config.php) and adjust if needed:
   - `db_host`
   - `db_name`
   - `db_user`
   - `db_pass`
   - `app_root_url`
   - `app_base_url`

## Open in browser

- Main URL: `http://localhost/Web1/E-commerce/public/index.php`
- Optional root shortcut (redirect): `http://localhost/Web1/E-commerce/`

## Where frontend is

- Pages (HTML rendered by PHP): [public](public)
- Shared header/footer: [app/Views/partials](app/Views/partials)
- Styles: [assets/css/style.css](assets/css/style.css)
- Frontend JS: [assets/js/app.js](assets/js/app.js)
