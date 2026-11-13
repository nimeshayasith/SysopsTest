terraform {
  required_version = ">= 1.6.0"

  required_providers {
    aws = {
      source  = "hashicorp/aws"
      version = ">= 5.0, < 7.0"
    }
  }
}

provider "aws" {
  region = var.aws_region

  # Applied to every taggable resource managed by this provider.
  default_tags {
    tags = {
      Project   = "SysOpsTest"
      Owner     = "Nimesha Yasith"
      ManagedBy = "Terraform"
    }
  }
}
