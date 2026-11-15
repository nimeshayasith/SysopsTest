# Canonical publishes the current Ubuntu 24.04 LTS AMI ID through SSM.
# The parameter resolves to an AMI in the selected AWS region.
data "aws_ssm_parameter" "ubuntu_2404_ami" {
  name = "/aws/service/canonical/ubuntu/server/24.04/stable/current/amd64/hvm/ebs-gp3/ami-id"
}

module "network" {
  source = "./modules/network"

  vpc_name = "${var.name_prefix}-vpc"
  vpc_cidr = "10.10.0.0/16"

  public_subnets = {
    public_a = {
      name              = "${var.name_prefix}-public-a"
      cidr              = "10.10.1.0/24"
      availability_zone = "ap-southeast-1a"
    }
    public_b = {
      name              = "${var.name_prefix}-public-b"
      cidr              = "10.10.2.0/24"
      availability_zone = "ap-southeast-1b"
    }
  }

  private_subnets = {
    private_a = {
      name              = "${var.name_prefix}-private-a"
      cidr              = "10.10.11.0/24"
      availability_zone = "ap-southeast-1a"
    }
    private_b = {
      name              = "${var.name_prefix}-private-b"
      cidr              = "10.10.12.0/24"
      availability_zone = "ap-southeast-1b"
    }
  }
}

module "security" {
  source = "./modules/security"

  name_prefix    = var.name_prefix
  vpc_id         = module.network.vpc_id
  admin_ssh_cidr = var.admin_ssh_cidr
}

module "compute" {
  source = "./modules/compute"

  ami_id                = data.aws_ssm_parameter.ubuntu_2404_ami.value
  subnet_id             = module.network.public_subnet_ids["public_a"]
  web_security_group_id = module.security.web_security_group_id
  key_pair_name         = var.key_pair_name
  instance_name         = "sysops-web01"
  name_tag              = "${var.name_prefix}-web01"
  instance_type         = "t3.micro"
  root_volume_size      = 20
  root_volume_type      = "gp3"
  timezone              = "Asia/Colombo"

  # Ensure the public route and subnet associations exist before cloud-init runs.
  depends_on = [module.network]
}

# Task 9: A records, created only when a hosted zone ID is supplied.
resource "aws_route53_record" "sites" {
  for_each = var.hosted_zone_id == null ? toset([]) : toset(["website1", "website2", "website3"])

  zone_id = var.hosted_zone_id
  name    = "${each.key}.saberion.cloud"
  type    = "A"
  ttl     = 300
  records = [module.compute.elastic_ip]
}

module "database" {
  source = "./modules/database"

  db_identifier        = "${var.name_prefix}-db"
  db_name              = var.db_name
  db_username          = var.db_username
  db_password          = var.db_password
  db_subnet_ids        = values(module.network.private_subnet_ids)
  db_security_group_id = module.security.db_security_group_id
  instance_class       = "db.t3.micro"
  allocated_storage    = 20
  multi_az             = false
}
