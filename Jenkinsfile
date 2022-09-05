pipeline {
  agent any
  stages {
    stage('SCM Checkout') {
      steps {
        checkout scm
      }
    }
    stage('Build/Launch (Docker)') {
      steps {
        echo 'Build Docker Success.'
      }
    }
    stage('Test') {
      steps {
        echo 'Integration Test Module'
        echo 'End-to-End Test'
      }
    }
    stage('Deploy') {
      steps {
        echo 'Deploy Success.'

      }
    }
  }
}
