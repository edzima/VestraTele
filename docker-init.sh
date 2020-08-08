#!/bin/bash
echo "******************************************"
echo "What docker deployment version would you like to install ? "
options=("Development ====> YII SERVE(frontend) + YII SERVE(backend) + NGNX(reverse proxy) + MYSQL" "Production ====> NGNX(frontend)+ NGNX(backend) + NGNX(reverse proxy) + MYSQL" "Quit")
select opt in "${options[@]}"
do
    case $opt in
        "Development ====> YII SERVE(frontend) + YII SERVE(backend) + NGNX(reverse proxy) + MYSQL")
            echo "INSTALLING development environment ..."
            docker-compose -f docker-compose-development.yml up
            exit;
            ;;
        "Production ====> NGNX(frontend) + NGNX(backend) + NGNX(reverse proxy) + MYSQL")
            echo "INSTALLING production environment ..."
            docker-compose -f docker-compose-production.yml up
            exit;
            ;;
        "Quit")
            break
            ;;
        *)
          echo "invalid option $REPLY"
          ;;
    esac
done