# Multi-stage Dockerfile for TDD Workshop Documentation

# =============================================================================
# Stage 1: Build
# =============================================================================
FROM node:20-alpine AS builder

WORKDIR /app

# Install dependencies
COPY package.json package-lock.json* ./
RUN npm ci || npm install

# Copy source
COPY website ./website

# Build static site
RUN npm run docs:build

# =============================================================================
# Stage 2: Production (nginx)
# =============================================================================
FROM nginx:alpine AS production

# Copy built site
COPY --from=builder /app/website/.vitepress/dist /usr/share/nginx/html

# Custom nginx config for SPA routing
COPY nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]

# =============================================================================
# Stage 3: Development (optional, for live reload)
# =============================================================================
FROM node:20-alpine AS development

WORKDIR /app

# Install dependencies
COPY package.json package-lock.json* ./
RUN npm ci || npm install

# Copy source
COPY website ./website

EXPOSE 5173

CMD ["npm", "run", "docs:dev", "--", "--host", "0.0.0.0"]
