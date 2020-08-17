#!/bin/bash


OUTPUT_FILE=".env"

function mergeEnvs() {
   echo -n "" > $OUTPUT_FILE;

   arr=("$@")
   for i in "${arr[@]}";
      do
          echo "##### MERGED FROM $i #####" >> $OUTPUT_FILE;
          cat "$i" >> $OUTPUT_FILE;
          echo "" >> $OUTPUT_FILE;
      done

    # shellcheck disable=SC2145
    echo "merged ${arr[@]}"
}


echo "******************************************"
echo "What docker deployment version would you like to start ? "
options=("Development ====> YII SERVE(frontend) + YII SERVE(backend) + NGNX(reverse proxy) + MYSQL" "Production ====> APACHE(frontend)+ APACHE(backend) + NGNX(reverse proxy) + MYSQL" "Tests =====> PHP(YII) + MYSQL" "stop all docker containers"  "Remove containers" "Quit")
select opt in "${options[@]}"
do
    case $opt in
        "Development ====> YII SERVE(frontend) + YII SERVE(backend) + NGNX(reverse proxy) + MYSQL")
            echo "STARTING development environment ..."
            toMerge=("environments/base.env" "environments/development.env");
            mergeEnvs "${toMerge[@]}"
            docker-compose -f docker-compose-development.yml up
            exit;
            ;;
        "Production ====> APACHE(frontend)+ APACHE(backend) + NGNX(reverse proxy) + MYSQL")
            echo "STARTING production environment ..."
            toMerge=("environments/base.env" "environments/production.env");
            mergeEnvs "${toMerge[@]}"
            docker-compose -f docker-compose-production.yml up
            exit;
            ;;

        "Tests =====> PHP(YII) + MYSQL")
            echo "STARTING tests environment and performing tests..."
            toMerge=("environments/base.env" "environments/production.env");
            mergeEnvs "${toMerge[@]}"
            docker-compose -f docker-compose-tests.yml up
            exit;
        ;;
        "stop all docker containers")
            docker stop $(docker ps -aq)
            exit;
        ;;
        "Remove containers")
            echo "Removing containers"
            docker-compose -f docker-compose-development.yml down
            docker-compose -f docker-compose-production.yml down
            docker-compose -f docker-compose-tests.yml down
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
