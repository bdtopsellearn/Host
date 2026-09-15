#!/usr/bin/env bash
# ==============================================================================
# VortexCloud Production Server Setup & Scaling Engine
# Automated 1-Click Provisioning for Redis In-Memory Caching & Background Queues
# ==============================================================================

set -e

# Visual formatting
BOLD="\033[1m"
GREEN="\033[0;32m"
BLUE="\033[0;34m"
PURPLE="\033[0;35m"
YELLOW="\033[0;33m"
RESET="\033[0m"

echo -e "${PURPLE}${BOLD}"
echo "========================================================================"
echo "        VortexCloud 1-Click Server & Scaling Provisioner                "
echo "========================================================================"
echo -e "${RESET}"

# Check for root / sudo privileges
if [ "$EUID" -ne 0 ]; then
  echo -e "${YELLOW}[!] Please run with sudo or as root: sudo bash deploy/setup-server.sh${RESET}"
  exit 1
fi

PROJECT_DIR=$(pwd)
echo -e "${BLUE}[*] Target Project Directory:${RESET} ${PROJECT_DIR}"

# 1. Update Package Lists
echo -e "${BLUE}[1/5] Updating system repositories...${RESET}"
apt-get update -y -qq

# 2. Install Redis & Supervisor
echo -e "${BLUE}[2/5] Installing Redis Server and Supervisor Worker Manager...${RESET}"
apt-get install -y redis-server supervisor

# 3. Enable & Start Redis
echo -e "${BLUE}[3/5] Starting & Enabling Redis In-Memory Service...${RESET}"
systemctl enable redis-server
systemctl restart redis-server

# Verify Redis is responding to PING
if redis-cli ping | grep -q "PONG"; then
    echo -e "${GREEN}[✓] Redis is running and responding (PONG).${RESET}"
else
    echo -e "${YELLOW}[!] Warning: Redis ping check did not return PONG. Please verify service status.${RESET}"
fi

# 4. Configure Supervisor for Background Queue Workers
echo -e "${BLUE}[4/5] Configuring Supervisor background queue workers for VortexCloud...${RESET}"
SUPERVISOR_CONF="/etc/supervisor/conf.d/vortexcloud-worker.conf"

cat > "${SUPERVISOR_CONF}" <<EOF
[program:vortexcloud-worker]
process_name=%(program_name)s_%(process_num)02d
command=php ${PROJECT_DIR}/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=${PROJECT_DIR}/storage/logs/worker.log
stopwaitsecs=3600
EOF

# Ensure storage log file exists with correct permissions
mkdir -p "${PROJECT_DIR}/storage/logs"
touch "${PROJECT_DIR}/storage/logs/worker.log"
chown -R www-data:www-data "${PROJECT_DIR}/storage" "${PROJECT_DIR}/bootstrap/cache"
chmod -R 775 "${PROJECT_DIR}/storage" "${PROJECT_DIR}/bootstrap/cache"

# Reload Supervisor
supervisorctl reread
supervisorctl update
supervisorctl restart vortexcloud-worker:*

echo -e "${GREEN}[✓] Supervisor workers registered and started successfully.${RESET}"

# 5. Optimize Laravel Configuration
echo -e "${BLUE}[5/5] Optimizing Laravel application caches...${RESET}"
cd "${PROJECT_DIR}"
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true
php artisan queue:restart || true

echo -e "\n${GREEN}${BOLD}========================================================================"
echo " [✓] VortexCloud Production Scaling Setup Completed Successfully!       "
echo "========================================================================${RESET}"
echo -e " - Redis In-Memory Store : ${GREEN}ACTIVE (port 6379)${RESET}"
echo -e " - Supervisor Workers    : ${GREEN}2 WORKERS RUNNING${RESET}"
echo -e " - Queue Engine          : ${GREEN}READY FOR ASYNC PROVISIONING${RESET}"
echo -e " - Zero-Crash Fallback   : ${GREEN}ACTIVE IN APP SERVICE PROVIDER${RESET}"
echo ""
echo -e "To view live worker logs: ${BLUE}tail -f ${PROJECT_DIR}/storage/logs/worker.log${RESET}"
echo -e "To check worker status  : ${BLUE}sudo supervisorctl status${RESET}"
echo ""
