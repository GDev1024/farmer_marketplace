# Deployment Setup Guide

## Environment Variables Configuration

### Local Development Setup

1. **Copy the example environment file:**
   ```bash
   cp .env.example .env
   ```

2. **Configure your local .env file:**
   ```bash
   # Database Configuration
   DB_HOST=localhost
   DB_NAME=grenada_marketplace
   DB_USER=root
   DB_PASS=your_password

   # AWS Configuration (for local S3 testing)
   AWS_REGION=us-east-1
   AWS_S3_BUCKET=your-test-bucket
   AWS_ACCESS_KEY=your_access_key
   AWS_SECRET_KEY=your_secret_key

   # Payment Configuration
   STRIPE_PUBLISHABLE_KEY=pk_test_your_stripe_key
   STRIPE_SECRET_KEY=sk_test_your_stripe_secret
   PAYPAL_CLIENT_ID=your_paypal_client_id
   PAYPAL_CLIENT_SECRET=your_paypal_secret
   PAYPAL_MODE=sandbox
   ```

### AWS Elastic Beanstalk Deployment

#### 1. Environment Variables Setup
In your AWS Elastic Beanstalk environment, configure these environment variables:

**Database:**
- `DB_HOST`: Your RDS endpoint (e.g., `mydb.123456789012.us-east-1.rds.amazonaws.com`)
- `DB_NAME`: `grenada_marketplace_prod`
- `DB_USER`: Your RDS username
- `DB_PASS`: Your RDS password

**Application:**
- `APP_ENV`: `production`
- `SITE_URL`: Your domain (e.g., `https://marketplace.yourdomain.com`)
- `SITE_NAME`: `Grenada Farmer Marketplace`

**AWS Services:**
- `AWS_REGION`: `us-east-1` (or your preferred region)
- `AWS_S3_BUCKET`: Your production S3 bucket name

**Payment (Production):**
- `STRIPE_PUBLISHABLE_KEY`: Your live Stripe publishable key
- `STRIPE_SECRET_KEY`: Your live Stripe secret key
- `PAYPAL_CLIENT_ID`: Your live PayPal client ID
- `PAYPAL_CLIENT_SECRET`: Your live PayPal client secret
- `PAYPAL_MODE`: `live`

#### 2. IAM Roles for AWS Services
Create an IAM role for your Elastic Beanstalk instance with these permissions:

```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Effect": "Allow",
            "Action": [
                "s3:GetObject",
                "s3:PutObject",
                "s3:DeleteObject"
            ],
            "Resource": "arn:aws:s3:::your-bucket-name/*"
        },
        {
            "Effect": "Allow",
            "Action": [
                "s3:ListBucket"
            ],
            "Resource": "arn:aws:s3:::your-bucket-name"
        }
    ]
}
```

#### 3. RDS Database Setup
1. Create an RDS MySQL instance
2. Configure security groups to allow access from your EB environment
3. Import the database schema using the provided SQL file

#### 4. S3 Bucket Setup
1. Create an S3 bucket for image storage
2. Configure CORS policy:
   ```json
   [
       {
           "AllowedHeaders": ["*"],
           "AllowedMethods": ["GET", "PUT", "POST", "DELETE"],
           "AllowedOrigins": ["https://your-domain.com"],
           "ExposeHeaders": []
       }
   ]
   ```

### Security Best Practices

1. **Never commit .env files** - They're in .gitignore for a reason
2. **Use strong passwords** for database and admin accounts
3. **Enable SSL/TLS** for your domain
4. **Rotate API keys** regularly
5. **Use IAM roles** instead of hardcoded AWS credentials in production
6. **Enable CloudTrail** for audit logging
7. **Set up monitoring** with CloudWatch

### Environment Variable Priority

The application loads environment variables in this order:
1. System environment variables (AWS EB)
2. .env file (local development)
3. Default values (fallback)

### Testing Your Setup

1. **Local Testing:**
   ```bash
   # Test database connection
   php -r "require 'root/includes/config.php'; var_dump(Config::getDB());"
   
   # Test AWS S3 (if configured)
   php -r "require 'root/includes/aws-image-handler.php'; echo 'AWS configured';"
   ```

2. **Production Testing:**
   - Verify all environment variables are set in EB console
   - Test database connectivity
   - Test image upload functionality
   - Test payment processing in sandbox mode first

### Troubleshooting

**Common Issues:**

1. **Database Connection Failed:**
   - Check RDS security groups
   - Verify DB_HOST, DB_USER, DB_PASS values
   - Ensure RDS is in same VPC as EB environment

2. **Image Upload Errors:**
   - Verify S3 bucket permissions
   - Check IAM role attached to EB environment
   - Ensure bucket exists and is accessible

3. **Payment Processing Errors:**
   - Verify API keys are correct
   - Check PAYPAL_MODE setting (sandbox vs live)
   - Ensure webhook endpoints are configured

### Monitoring and Logs

- **Application Logs:** Available in EB console under "Logs"
- **Database Logs:** Available in RDS console
- **S3 Access Logs:** Can be enabled in S3 bucket settings
- **Payment Logs:** Available in Stripe/PayPal dashboards

For additional support, refer to the AWS documentation or contact your system administrator.