# 🚜 Farmer Marketplace App – Capstone Project 2026

[![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat&logo=php&logoColor=white)](https://www.php.net/)  
[![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=flat&logo=javascript&logoColor=black)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)

---

## 📌 Project Title  
**Farmer Marketplace App & Website** – A platform connecting Grenadian farmers directly with consumers to buy and sell fresh produce efficiently.
---

## 🌱 Local Context  
In Grenada, small farmers play a vital role in supplying fresh produce, but supply-demand gaps often lead to food waste or inconsistent access for consumers. This marketplace helps bridge that gap by enabling direct trade, supporting local agriculture, and reducing waste.

---

## 📝 Project Description  
The Farmer Marketplace App & Website is a **full-stack application** where users can:

- Browse farm products  
- Create product listings  
- Place and track orders  

Farmers can optionally **verify their accounts** by submitting a Farmer ID, giving them a “verified” status. The frontend is built with JavaScript / PHP, and the backend is a **PHP + MySQL API** managed via **phpMyAdmin**.


---

## 🎯 Objectives  
1. Build a working frontend + backend for a local farmer marketplace  
2. Enable farmer verification via Farmer ID  
3. Implement CRUD functionality for product listings  
4. Support order creation, tracking, and reviews  
5. Persist all data securely in a MySQL database

---

## ⚡ Features  
- 👤 User profiles (customers & farmers)  
- ✅ Farmer verification with Farmer ID  
- 📦 Product listings: create, read, update, delete  
- 🛒 Browse products and place orders  
- 🔄 Order tracking and status updates  
- 💬 Messaging between users  
- ⭐ Product reviews  
- 🔔 Notifications for users  
- 💳 **Secure Payment Processing** (Stripe & PayPal)
- 🖼️ **AWS S3 Image Storage** with local fallback
- 🎨 **Facebook-Inspired UI** with glassmorphism effects
- 📱 **Responsive Design** for mobile and desktop
- ☁️ **AWS Deployment Ready** with Elastic Beanstalk support
- 🧾 Data stored in MySQL backend via PHP API  

---

## 🛠️ Technology Stack  
- **Frontend / Website**: PHP, HTML, CSS, JavaScript  
- **Backend / API**: PHP  
- **Database**: MySQL (via phpMyAdmin)  
- **Payment Processing**: Stripe & PayPal APIs
- **Image Storage**: AWS S3 with CloudFront CDN (optional)
- **Deployment**: AWS Elastic Beanstalk, RDS, S3
- **Development Server**: Local (XAMPP) or AWS

---

## 🗄️ Database Overview
The application uses a relational database to manage its core entities:

- **Users**: Stores user profiles, optional farmer verification, and contact information.  
- **Listings**: Contains product information including name, category, price, quantity, and status.  
- **Orders & Order Items**: Tracks orders placed by customers and the specific products included.  
- **Messages**: Supports messaging between users.  
- **Reviews**: Stores ratings and reviews for completed orders.  
- **Notifications**: Tracks alerts and updates for users.  
- **Cart**: Optional structure for storing user cart items.

The database is fully normalized and uses indexes for efficient querying and relational integrity.

---

## 📚 Documentation

- **[Implementation Guide](IMPLEMENTATION_GUIDE.md)** - Detailed technical implementation and architecture
- **[AWS Deployment Guide](AWS_DEPLOYMENT_GUIDE.md)** - Complete AWS deployment instructions
- **[License](LICENSE.md)** - MIT License terms

---

## Payment Integration

The application supports secure payment processing through both Stripe and PayPal:

### Stripe Integration
- Credit/debit card payments with Stripe Elements
- Secure tokenization and PCI compliance
- Real-time payment verification
- Support for multiple currencies

### PayPal Integration  
- PayPal account payments
- Redirect-based payment flow
- Automatic payment capture
- Sandbox and live environment support

### Setup Instructions
1. Copy `.env.example` to `.env`
2. Add your Stripe API keys:
   ```
   STRIPE_PUBLISHABLE_KEY=pk_test_your_key_here
   STRIPE_SECRET_KEY=sk_test_your_key_here
   ```
3. Add your PayPal credentials:
   ```
   PAYPAL_CLIENT_ID=your_client_id_here
   PAYPAL_CLIENT_SECRET=your_client_secret_here
   PAYPAL_MODE=sandbox
   ```
4. Run database migrations to add payment tracking tables

### Payment Flow
1. Users add items to cart
2. Proceed to secure checkout page
3. Choose payment method (Stripe or PayPal)
4. Complete payment with chosen provider
5. Order is created and inventory is updated
6. Payment confirmation and order tracking

---

## 📊 Project Evaluation

### ✅ Achievements
- Fully functional frontend integrated with a PHP + MySQL backend  
- Farmer verification (partly) implemented via optional Farmer ID  
- Secure, persistent CRUD operations for users, listings, orders, and messages  
- Clear, responsive UI across multiple pages  
- **Complete payment integration** with Stripe and PayPal support
- **AWS-ready deployment** with S3 image storage and Elastic Beanstalk configuration
- **Modern Facebook-inspired UI** with glassmorphism effects and responsive design
- **Comprehensive order management** with payment tracking and status updates
- **Professional documentation** with deployment and implementation guides

