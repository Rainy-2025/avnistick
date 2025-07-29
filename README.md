# Avnistick Server API Documentation

This document provides a comprehensive overview of the RESTful APIs available in the Avnistick Server backend. It covers authentication, category management, product management, contact, services, gallery, landing page, and other related APIs. Example request and response data are included to facilitate testing and integration.

---

## Table of Contents

- [Authentication](#authentication)
  - [Admin Authentication](#admin-authentication)
  - [User Authentication](#user-authentication)
- [Category Management](#category-management)
- [Product Management](#product-management)
  - [Ready-made and Semi-customizable Products](#ready-made-and-semi-customizable-products)
  - [Customizable Products](#customizable-products)
  - [All Products](#all-products)
- [Contact](#contact)
- [Services and Subservices](#services-and-subservices)
- [Gallery Management](#gallery-management)
- [Landing Page Management](#landing-page-management)
- [Testing Data Examples](#testing-data-examples)
- [Authentication Notes](#authentication-notes)

---

## Authentication

### Admin Authentication

- **POST** `/admin/login`  
  Login as admin.  
  **Request Body:**  
  ```json
  {
    "email": "admin@example.com",
    "password": "password123"
  }
  ```  
  **Response:**  
  ```json
  {
    "status": true,
    "message": "Login successful.",
    "data": {
      "token": "admin-access-token",
      "admin": { /* admin user object */ }
    }
  }
  ```

- **POST** `/admin/logout`  
  Logout admin (requires auth token).

- **GET** `/admin/profile`  
  Get admin profile (requires auth token).

---

### User Authentication

- **POST** `/send-otp`  
  Send OTP to email for registration.  
  **Request Body:**  
  ```json
  {
    "name": "User Name",
    "email": "user@example.com",
    "password": "password123"
  }
  ```  
  **Response:**  
  ```json
  {
    "status": true,
    "message": "OTP sent to your email."
  }
  ```

- **POST** `/verify-otp`  
  Verify OTP and register user.  
  **Request Body:**  
  ```json
  {
    "email": "user@example.com",
    "otp": "123456"
  }
  ```  
  **Response:**  
  ```json
  {
    "status": true,
    "message": "Email verified and user registered.",
    "access_token": "user-access-token",
    "token_type": "Bearer",
    "user": { /* user object */ }
  }
  ```

- **POST** `/login`  
  User login.  
  **Request Body:**  
  ```json
  {
    "email": "user@example.com",
    "password": "password123"
  }
  ```  
  **Response:**  
  ```json
  {
    "status": true,
    "message": "Login successful.",
    "access_token": "user-access-token",
    "token_type": "Bearer",
    "user": { /* user object */ }
  }
  ```

- **POST** `/logout`  
  Logout user (requires auth token).

- **GET** `/profile`  
  Get user profile (requires auth token).

---

## Category Management

- **POST** `/categories`  
  Add a new category (admin only).  
  **Request Body:**  
  ```json
  {
    "name": "Category Name"
  }
  ```

- **GET** `/categories`  
  Get all categories with subcategories (admin).

- **PUT** `/categories/{id}`  
  Update category name (admin).

- **DELETE** `/categories/{id}`  
  Delete category (admin).

- **POST** `/categories/{id}/subcategories`  
  Add subcategory under category (admin).  
  Supports image upload (multipart/form-data).

- **PUT** `/subcategories/{id}`  
  Update subcategory (name + image) (admin).

- **DELETE** `/subcategories/{id}`  
  Delete subcategory (admin).

- **GET** `/user/categories`  
  Get categories for user frontend.

- **GET** `/user/navbar_categories`  
  Get categories for navbar (user).

- **GET** `/user/categories/{id}/subcategories`  
  Get subcategories by category ID (user).  
  If `id` is 0, returns all subcategories.

---

## Product Management

### Ready-made and Semi-customizable Products

- **POST** `/readymade-products`  
  Create a product (admin).  
  Supports fields: name, type (readymade/semi_customizable), category_id, subcategory_id, descriptions, images, sizes, attributes, properties.

- **GET** `/readymade-products`  
  Get all products (admin).

- **GET** `/readymade-products/{id}`  
  Get product by ID (admin).

- **PUT** `/readymade-products/{id}`  
  Update product (admin).

- **DELETE** `/readymade-products/{id}`  
  Delete product (admin).

- **GET** `/user/readymade-products`  
  Get ready-made products (user).

---

### Customizable Products

- **POST** `/customize-products`  
  Create customizable product (admin).

- **GET** `/customize-products`  
  Get all customizable products (admin).

- **GET** `/customize-products/{id}`  
  Get customizable product by ID (admin).

- **DELETE** `/customize-products/{id}`  
  Delete customizable product (admin).

- **GET** `/user/customize-products`  
  Get customizable products (user).

---

### All Products

- **GET** `/all-products`  
  Get all products with optional filters:  
  - `type`: `readymade`, `semi_customizable`, or `customize`  
  - `category_id`  
  - `subcategory_id`  
  - `search` (name search)  

---

## Contact

- **POST** `/user/contact`  
  Submit contact form (user).  
  Sends email notification to admin.

- **GET** `/admin/contacts`  
  Get all contacts (admin).

- **PUT** `/admin/contacts/{id}`  
  Update contact (admin).

- **DELETE** `/admin/contacts/{id}`  
  Delete contact (admin).

---

## Services and Subservices

- **POST** `/services`  
  Add service (admin).

- **GET** `/services`  
  Get all services with subservices (admin and user).

- **PUT** `/services/{id}`  
  Update service (admin).

- **DELETE** `/services/{id}`  
  Delete service (admin).

- **POST** `/services/{id}/subservices`  
  Add subservice to service (admin).

- **PUT** `/subservices/{id}`  
  Update subservice (admin).

- **DELETE** `/subservices/{id}`  
  Delete subservice (admin).

- **GET** `/user/services`  
  Get services with subservices (user).

- **GET** `/user/services/{id}/subservices`  
  Get subservices by service ID (user).

---

## Gallery Management

- Instagram Images (max 5)  
  - **POST** `/upload-instagram`  
  - **GET** `/instagram`  
  - **DELETE** `/instagram/{id}`  

- Art Section  
  - **POST** `/upload-art`  
  - **GET** `/art`  
  - **DELETE** `/art/{id}`  

- Gallery Images  
  - **POST** `/upload-gallery`  
  - **GET** `/gallery`  
  - **DELETE** `/gallery/{id}`  

- Artists (max 5)  
  - **POST** `/upload-artist`  
  - **GET** `/artists`  
  - **DELETE** `/artist/{id}`  

---

## Landing Page Management

- Banners  
  - **POST** `/admin/banner`  
  - **GET** `/admin/banner`  
  - **POST** `/admin/banner/{id}`  
  - **DELETE** `/admin/banner/{id}`  

- Brands  
  - **POST** `/admin/brand`  
  - **GET** `/admin/brand`  
  - **POST** `/admin/brand/{id}`  

- User-facing:  
  - **GET** `/user/banners`  
  - **GET** `/user/brand`  

---

## Testing Data Examples

### Admin Login

```bash
curl -X POST http://yourdomain.com/api/admin/login \
-H "Content-Type: application/json" \
-d '{"email":"admin@example.com","password":"password123"}'
```

### Create Category

```bash
curl -X POST http://yourdomain.com/api/categories \
-H "Authorization: Bearer {admin_token}" \
-H "Content-Type: application/json" \
-d '{"name":"New Category"}'
```

### Create Ready-made Product

```bash
curl -X POST http://yourdomain.com/api/readymade-products \
-H "Authorization: Bearer {admin_token}" \
-H "Content-Type: application/json" \
-d '{
  "name": "Product 1",
  "type": "readymade",
  "category_id": 1,
  "subcategory_id": 1,
  "short_description": "Short desc",
  "long_description": "Long desc",
  "images": ["image1.jpg", "image2.jpg"],
  "sizes": [{"size":"M","price":100,"stock":10}],
  "attributes": [{"heading":"Color","type":"string","value":"Red"}],
  "properties": "Some properties"
}'
```

### Send OTP (User Registration)

```bash
curl -X POST http://yourdomain.com/api/send-otp \
-H "Content-Type: application/json" \
-d '{"name":"User","email":"user@example.com","password":"password123"}'
```

---

## Authentication Notes

- Admin and user authentication use Sanctum tokens.
- Include `Authorization: Bearer {token}` header for protected routes.
- OTP is used for user registration verification.

---

This README provides a detailed overview of the API endpoints, their usage, and example data for testing. Use this as a reference for integrating with the Avnistick backend.
