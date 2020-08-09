#!/bin/bash
echo "******************************************"
echo "What docker deployment version would you like to start ? "
options=("Development ====> YII SERVE(frontend) + YII SERVE(backend) + NGNX(reverse proxy) + MYSQL" "Production ====> APACHE(frontend)+ APACHE(backend) + NGNX(reverse proxy) + MYSQL" "Stop Development" "Quit")
select opt in "${options[@]}"
do
    case $opt in
        "Development ====> YII SERVE(frontend) + YII SERVE(backend) + NGNX(reverse proxy) + MYSQL")
            echo "STARTING development environment ..."
            docker-compose -f docker-compose-development.yml up
            exit;
            ;;
        "Production ====> APACHE(frontend)+ APACHE(backend) + NGNX(reverse proxy) + MYSQL")
            echo "STARTING production environment ..."
            docker-compose -f docker-compose-production.yml up
            exit;
            ;;
        "Stop Development")
            echo "Removing development containers"
            docker-compose -f docker-compose-development.yml down
            docker-compose -f docker-compose-production.yml down
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