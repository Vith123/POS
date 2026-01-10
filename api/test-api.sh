#!/bin/bash

# 🧪 POS API Test Script
# This script tests the authentication endpoints

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🧪 Testing POS System API"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# Test 1: Login as Admin
echo "📍 TEST 1: Admin Login"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

RESPONSE=$(curl -s -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "admin@pos.com",
    "password": "password"
  }')

echo "$RESPONSE" | jq '.'

# Extract token
TOKEN=$(echo "$RESPONSE" | jq -r '.token')

echo ""
echo "✅ Token: ${TOKEN:0:20}..."
echo ""

# Test 2: Get Current User
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📍 TEST 2: Get Current User Info"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

curl -s -X GET http://localhost:8000/api/me \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" | jq '.'

echo ""

# Test 3: Login as Cashier
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📍 TEST 3: Cashier Login"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

curl -s -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "cashier@pos.com",
    "password": "password"
  }' | jq '.'

echo ""

# Test 4: Invalid Login
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📍 TEST 4: Invalid Login (Should Fail)"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

curl -s -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "wrong@email.com",
    "password": "wrongpassword"
  }' | jq '.'

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✅ All Tests Complete!"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
