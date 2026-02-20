#!/bin/bash

# PPMS Queue Worker Startup Script
# This script starts the Laravel queue worker for development/testing

echo "Starting PPMS Queue Worker..."
echo "Queue Connection: database"
echo "Press Ctrl+C to stop"
echo "---"

cd "$(dirname "$0")"
php artisan queue:work database --verbose --tries=3 --timeout=90
