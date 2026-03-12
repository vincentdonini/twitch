FROM node:22

RUN corepack enable && corepack prepare pnpm@9.0.6 --activate

WORKDIR /app

# Copy workspace manifest files only (for dependency install layer)
COPY front/pnpm-lock.yaml front/pnpm-workspace.yaml front/.npmrc front/package.json ./
COPY front/apps/web/package.json ./apps/web/
COPY front/apps/admin/package.json ./apps/admin/
COPY front/packages/eslint-config/package.json ./packages/eslint-config/
COPY front/packages/typescript-config/package.json ./packages/typescript-config/
COPY front/packages/ui/package.json ./packages/ui/
COPY front/packages/api/package.json ./packages/api/

# Install all dependencies (Linux binaries compiled here)
RUN pnpm install --frozen-lockfile

# Source code is bind-mounted at runtime
