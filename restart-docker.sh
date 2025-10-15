#!/bin/bash
# rebuild.sh - Rebuild and restart docker-compose stack

set -e

echo "📦 Stopping containers..."
docker compose down

echo "🔨 Building images..."
docker compose up -d --build

echo "✅ Done! Containers are up and running."
