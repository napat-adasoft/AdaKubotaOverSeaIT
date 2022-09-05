pipeline {
  agent any
  stages {
    stage('Checkout') {
      steps {
        git(url: 'https://github.com/napat-adasoft/AdaKubotaOverSeaIT.git', branch: 'Production')
      }
    }
    stage('Release') {
      steps {
        echo 'Ready to release etc.'
      }
    }
  }
}
