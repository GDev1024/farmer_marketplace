# Grenada Farmer Marketplace - Documentation

This directory contains comprehensive PlantUML diagrams documenting the architecture, design, and user flows of the Grenada Farmer Marketplace application.

## 📋 Documentation Overview

### 🗺️ [Sitemap](sitemap.puml)
Complete website sitemap showing all pages, their relationships, and navigation flows. Includes:
- Landing and public pages
- Authentication flow
- Farmer-specific pages
- Customer journey pages
- API endpoints
- User role-based navigation

### 🎨 [Page Layouts](page-layouts.puml)
Detailed breakdown of different page layout structures:
- **Landing Page Layout**: Hero-driven conversion design
- **Auth Page Layout**: Clean, centered authentication forms
- **App Page Layout**: Full navigation with user-specific content
- **Product Detail Layout**: Two-column product showcase
- **Shopping Flow Layout**: Cart and checkout processes

### 👥 [User Flows](user-flows.puml)
User journey mapping for different user types:
- **Visitor Flow**: Landing → Registration → Login
- **Farmer Journey**: Product management and inventory
- **Customer Journey**: Browse → Cart → Checkout → Orders
- **Payment Process**: Dual payment method integration

### 🧩 [Component Architecture](component-architecture.puml)
Design system and component structure:
- **CSS Architecture**: Modular stylesheet organization
- **Component Hierarchy**: Reusable UI components
- **Responsive Design**: Mobile-first breakpoint system
- **Design Tokens**: Color palette, typography, spacing

### 🗄️ [Database ERD](database-erd.puml)
Entity Relationship Diagram showing:
- **Core Entities**: Users, Listings
- **Transaction Entities**: Orders, Cart, Order Items
- **Communication Entities**: Messages, Reviews
- **System Entities**: Notifications
- **Relationships**: Foreign keys and constraints
- **Business Rules**: Data validation and logic

### 🔌 [API Architecture](api-architecture.puml)
Backend API structure and endpoints:
- **Authentication API**: Login, register, logout
- **Products API**: CRUD operations for listings
- **Cart API**: Shopping cart management
- **Payment API**: Stripe and PayPal integration
- **External Services**: AWS S3, payment gateways
- **Security**: Authentication, validation, encryption

## 🛠️ How to Use These Diagrams

### Viewing PlantUML Diagrams

1. **Online Viewer**: Copy the content of any `.puml` file to [PlantUML Online Server](http://www.plantuml.com/plantuml/uml/)

2. **VS Code Extension**: Install the "PlantUML" extension and preview files directly in the editor

3. **Local Installation**: 
   ```bash
   # Install PlantUML
   npm install -g node-plantuml
   
   # Generate PNG from PUML
   puml generate sitemap.puml
   ```

### Diagram Categories

| Diagram | Purpose | Audience |
|---------|---------|----------|
| **Sitemap** | Navigation structure | Developers, UX Designers |
| **Page Layouts** | UI structure | Frontend Developers, Designers |
| **User Flows** | User experience | UX Designers, Product Managers |
| **Component Architecture** | Design system | Frontend Developers |
| **Database ERD** | Data structure | Backend Developers, DBAs |
| **API Architecture** | Backend structure | Backend Developers, DevOps |

## 🎯 Key Design Principles

### User Experience
- **Mobile-First**: Responsive design optimized for all devices
- **Conversion-Focused**: Landing page designed for user registration
- **Role-Based UX**: Different interfaces for farmers vs. customers
- **Accessibility**: WCAG 2.1 compliant design patterns

### Technical Architecture
- **Modular CSS**: Organized stylesheet architecture with design tokens
- **Component-Based**: Reusable UI components across pages
- **Secure API**: RESTful endpoints with proper authentication
- **Scalable Database**: Normalized schema with proper indexing

### Business Logic
- **Dual User Types**: Farmers (sellers) and customers (buyers)
- **Product Management**: Complete CRUD for farmer listings
- **Shopping Experience**: Cart, checkout, and order tracking
- **Payment Integration**: Stripe and PayPal support

## 📱 Responsive Design Strategy

The application follows a mobile-first approach with these breakpoints:
- **Mobile**: 320px+ (Primary focus)
- **Tablet**: 768px+ (Enhanced layout)
- **Desktop**: 1024px+ (Full features)
- **Large**: 1200px+ (Optimized spacing)

## 🔒 Security Considerations

- **Authentication**: bcrypt password hashing, secure sessions
- **Input Validation**: Server-side validation for all inputs
- **SQL Injection Prevention**: PDO prepared statements
- **Payment Security**: PCI-compliant payment processing
- **File Upload Security**: Validated image uploads to AWS S3

## 🚀 Deployment Architecture

The application is designed for AWS deployment:
- **Elastic Beanstalk**: Application hosting
- **RDS MySQL**: Database hosting
- **S3**: Image storage with CloudFront CDN
- **Environment Variables**: Secure configuration management

## 📊 Performance Optimization

- **Efficient CSS**: Modular architecture with minimal render-blocking
- **Image Optimization**: Automatic resizing and compression
- **Database Indexing**: Optimized queries for fast data retrieval
- **Caching Strategy**: Browser caching for static assets

---

For technical implementation details, refer to the main project documentation in the root directory.