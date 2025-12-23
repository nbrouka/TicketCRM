#!/bin/bash

# Script to run code analysis tools and tests

set -e # Exit immediately if a command exits with a non-zero status

echo "Starting code analysis and tests..."

# Function to run pint
run_pint() {
    echo "Running Laravel Pint..."
    if docker exec laravel-app ./vendor/bin/pint --test; then
        echo "✓ Pint check passed - no style issues found"
    else
        echo "✗ Pint check failed - running auto-fix..."
        docker exec laravel-app ./vendor/bin/pint
        echo "✓ Pint auto-fix completed"
    fi
}

# Function to run phpstan
run_phpstan() {
    echo "Running PHPStan analysis..."
    if docker exec laravel-app ./vendor/bin/phpstan analyse; then
        echo "✓ PHPStan analysis passed - no errors found"
    else
        echo "✗ PHPStan analysis failed"
        exit 1
    fi
}

# Function to run tests
run_tests() {
    echo "Running PHPUnit tests..."
    if docker exec laravel-app php artisan test; then
        echo "✓ All tests passed"
    else
        echo "✗ Some tests failed"
        exit 1
    fi
}

# Check if Docker containers are running
if ! docker ps | grep -q laravel-app; then
    echo "Starting Docker containers..."
    docker-compose up -d
    echo "Waiting for containers to be ready..."
    sleep 10
fi

# Run all checks
run_pint
run_phpstan
run_tests

echo "All checks completed successfully!"