pipeline {
  agent {
    docker {
      image 'arabstok-cicd_php'
      args '--network arabstok-cicd_cicd'
    }

  }
  stages {
    stage('Build') {
      steps {
        echo 'Start Build Step'
        sh 'cp /mnt/env .env'
        sh 'php --version'
        sh 'composer --version'
        sh 'composer install'
        sh 'php artisan key:generate'
        sh 'php artisan migrate'
        echo 'Build Step Complite'
      }
    }

    stage('Stage Deploy') {
      steps {
        echo 'Initiating Deployment'
        sh 'cp -r /var/jenkins_home/workspace/arabsstock_stage/* /mnt/arabsstock/'
        sh ' chmod -R 777 /mnt/arabsstock/storage && chmod -R 777 /mnt/arabsstock/bootstrap/cache'
        sh 'cp /mnt/env /mnt/arabsstock/.env'
      }
    }

  }
}