# Simple HTTP Methods (Beginner Version)

This is a separate, beginner-friendly mini project.

It focuses on understanding HTTP methods in PHP:

- GET: read pages/data
- POST: create/submit actions
- PUT: update (simulated with `_method=PUT`)
- DELETE: delete (simulated with `_method=DELETE`)

## Files

- `index.php`: entry page
- `products.php`: product list loaded with GET
- `cart.php`: add/remove/clear cart with POST
- `checkout.php`: basic checkout with POST
- `http_methods_lab.php`: playground for GET/POST/PUT/DELETE
- `includes/helpers.php`: shared beginner helper functions
- `includes/data.php`: small static product data

## Run with XAMPP

If your folder is at `C:/xampp/htdocs/E-commerce`, open:

- `http://localhost/E-commerce/simple-http-methods/index.php`

## Notes

- This folder intentionally avoids OOP complexity.
- It uses sessions and static data so beginners can focus on request methods first.
