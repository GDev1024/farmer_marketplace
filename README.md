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
- 👤 **Dual User Experience**: Adaptive interface for farmers (sellers) and customers (buyers)
- ✅ **Farmer Verification**: Optional Farmer ID verification with trusted badges
- 📦 **Product Management**: Complete CRUD functionality with visual product cards
- 🛒 **Shopping Experience**: Modern cart, checkout, and order tracking system
- 🎨 **Modern Design System**: Clean, professional UI with Playfair Display typography
- 📱 **Mobile-First Design**: Responsive interface optimized for all devices
- 🔐 **Secure Authentication**: bcrypt password hashing with role-based access
- 💳 **Payment Processing**: Integrated Stripe and PayPal checkout flows
- 🖼️ **AWS S3 Integration**: Cloud image storage with local development fallback
- ☁️ **Cloud-Ready Architecture**: Elastic Beanstalk deployment support
- 🎯 **Conversion-Focused Landing**: Clean homepage driving user registration
- 📊 **Dashboard Analytics**: Farmer insights and customer order history
- 🔄 **Real-Time Updates**: Dynamic inventory management and status tracking  

---

## 🛠️ Technology Stack  
- **Frontend**: Modern HTML5, CSS3 with design system, Vanilla JavaScript
- **Typography**: Playfair Display serif font with system font fallbacks
- **Styling**: Modular CSS architecture with design tokens and components
- **Backend**: PHP 8.1+ with object-oriented architecture
- **Database**: MySQL with PDO prepared statements for security
- **Authentication**: bcrypt password hashing with session management
- **Payment Processing**: Stripe Elements and PayPal SDK integration
- **Image Storage**: AWS S3 with automatic environment detection
- **Deployment**: AWS Elastic Beanstalk, RDS, S3, CloudFront CDN
- **Development**: Local XAMPP/WAMP with hot-reload capabilities

---

## 🗄️ Database Overview
The application uses a normalized relational database following Third Normal Form (3NF) principles:

**Core Tables:**
- **Users**: User profiles with role-based access (farmer/customer) and optional verification
- **Listings**: Product catalog with images, pricing, inventory, and status management
- **Orders & Order Items**: Complete order lifecycle with payment tracking and item details
- **Cart**: Session-based shopping cart with quantity management
- **Messages**: Direct communication system between farmers and customers
- **Reviews**: Product and farmer rating system with feedback
- **Notifications**: Real-time alerts for orders, messages, and system updates

**Key Features:**
- Referential integrity with foreign key constraints
- Indexed queries for optimal performance
- Atomic transactions for order processing
- Secure data validation and sanitization

---

## 🎨 Design System

### Modern UI Architecture
- **Typography**: Playfair Display for headings, system fonts for optimal performance
- **Color Palette**: Primary green (#2d5016) with Caribbean-inspired accents
- **Component Library**: Unified buttons, cards, forms, modals, and navigation
- **Layout System**: CSS Grid and Flexbox with consistent spacing tokens
- **Responsive Framework**: Mobile-first approach with breakpoint system

### User Experience
- **Landing Page**: Conversion-focused design with clear value propositions
- **Authentication Flow**: Clean login/register with proper validation
- **Dual Dashboards**: Farmer product management vs. customer browsing interface
- **Shopping Experience**: Modern cart, checkout, and order tracking
- **Mobile Optimization**: Touch-friendly with 44px minimum button sizes

### Accessibility & Performance
- **WCAG 2.1 Compliance**: Proper contrast ratios and keyboard navigation
- **Progressive Enhancement**: Core functionality works on all devices
- **Loading Optimization**: Efficient CSS architecture and image handling
- **Cross-Browser Support**: Tested on Chrome, Safari, Firefox

---

## 📚 Documentation

- **[Implementation Guide](IMPLEMENTATION_GUIDE.md)** - Technical architecture and design system details
- **[AWS Deployment Guide](AWS_DEPLOYMENT_GUIDE.md)** - Complete cloud deployment instructions
- **[License](LICENSE.md)** - MIT License terms

---

## 🚀 Quick Start

### Local Development Setup
1. **Prerequisites**: PHP 8.1+, MySQL, XAMPP/WAMP
2. **Clone Repository**: `git clone https://github.com/GDev1024/farmer_marketplace.git`
3. **Database Setup**: Import `includes/grenada_marketplace.sql`
4. **Configuration**: Update `includes/config.php` with your database credentials
5. **Launch**: Start Apache and MySQL, navigate to `localhost/farmer_marketplace`

### User Roles
- **Farmers**: Register as seller to manage product listings and inventory
- **Customers**: Register as buyer to browse products and place orders
- **Admin**: Database management through phpMyAdmin interface

---

## 💳 Payment Integration

### Supported Payment Methods
- **Stripe**: Credit/debit cards with Elements UI and PCI compliance
- **PayPal**: Account-based payments with redirect flow
- **Security**: Tokenized payments with server-side validation

### Configuration
```bash
# Environment Variables (.env)
STRIPE_PUBLISHABLE_KEY=pk_test_your_key_here
STRIPE_SECRET_KEY=sk_test_your_key_here
PAYPAL_CLIENT_ID=your_client_id_here
PAYPAL_CLIENT_SECRET=your_client_secret_here
PAYPAL_MODE=sandbox
```

### Checkout Flow
1. **Cart Management**: Add/remove items with real-time totals
2. **Address Collection**: Grenadian parish-based delivery system
3. **Payment Selection**: Choose between Stripe or PayPal
4. **Order Processing**: Atomic transactions with inventory updates
5. **Confirmation**: Order tracking and email notifications

---

## 📊 Project Evaluation

### ✅ Technical Achievements
- **Full-Stack Implementation**: Complete PHP/MySQL backend with modern frontend
- **Design System**: Professional UI with consistent components and typography
- **User Experience**: Dual interfaces optimized for farmers and customers
- **Security**: bcrypt authentication, PDO prepared statements, input validation
- **Cloud Integration**: AWS S3 image storage with automatic environment detection
- **Payment Processing**: Integrated Stripe and PayPal with secure checkout flows
- **Mobile Optimization**: Responsive design tested across all device sizes
- **Performance**: Optimized CSS architecture and efficient database queries

### 🎯 Business Impact
- **Local Agriculture Support**: Direct farmer-to-consumer marketplace
- **Reduced Food Waste**: Better supply-demand matching through digital platform
- **Increased Farmer Income**: Elimination of intermediary markups
- **Community Building**: Platform connecting local producers with consumers
- **Scalable Solution**: Architecture ready for expansion across Caribbean markets

### 🔧 Development Highlights
- **Modern Architecture**: Modular PHP with separation of concerns
- **Database Design**: Normalized schema with referential integrity
- **API Development**: RESTful endpoints for all major operations
- **Testing Coverage**: Cross-browser compatibility and mobile responsiveness
- **Documentation**: Comprehensive guides for deployment and maintenance

