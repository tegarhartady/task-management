#!/bin/bash

# Vuexy Admin - Authentication Setup Script
# Automated setup untuk sistem login dan role management

echo "════════════════════════════════════════════════════"
echo "  Vuexy Admin - Authentication Setup"
echo "════════════════════════════════════════════════════"
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Step 1: Generate app key
echo -e "${YELLOW}Step 1/5: Generate APP_KEY${NC}"
php artisan key:generate
echo -e "${GREEN}✓ APP_KEY generated${NC}"
echo ""

# Step 2: Run migrations
echo -e "${YELLOW}Step 2/5: Running Database Migrations${NC}"
php artisan migrate --force
echo -e "${GREEN}✓ Migrations completed${NC}"
echo ""

# Step 3: Run seeders
echo -e "${YELLOW}Step 3/5: Seeding Database with Users${NC}"
php artisan db:seed --class=UserSeeder
echo -e "${GREEN}✓ Seeder completed${NC}"
echo ""

# Step 4: Clear cache
echo -e "${YELLOW}Step 4/5: Clearing Cache${NC}"
php artisan cache:clear
php artisan route:clear
php artisan config:clear
echo -e "${GREEN}✓ Cache cleared${NC}"
echo ""

# Step 5: Storage permissions
echo -e "${YELLOW}Step 5/5: Setting Storage Permissions${NC}"
chmod -R 755 storage
chmod -R 755 bootstrap/cache
echo -e "${GREEN}✓ Permissions set${NC}"
echo ""

echo "════════════════════════════════════════════════════"
echo -e "${GREEN}Setup Completed Successfully!${NC}"
echo "════════════════════════════════════════════════════"
echo ""
echo "📝 Test Credentials:"
echo ""
echo "   ADMIN:"
echo "   Email: admin@example.com"
echo "   Password: password123"
echo ""
echo "   SUPERVISOR:"
echo "   Email: supervisor@example.com"
echo "   Password: password123"
echo ""
echo "   MANAGER:"
echo "   Email: manager@example.com"
echo "   Password: password123"
echo ""
echo "   KARYAWAN:"
echo "   Email: dila@example.com"
echo "   Password: password123"
echo ""
echo "🚀 Next steps:"
echo "   1. Run: php artisan serve"
echo "   2. Open: http://localhost:8000/login"
echo "   3. Login with any of the credentials above"
echo ""
