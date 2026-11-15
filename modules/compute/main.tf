resource "aws_instance" "web" {
  ami                         = var.ami_id
  instance_type               = var.instance_type
  subnet_id                   = var.subnet_id
  vpc_security_group_ids      = [var.web_security_group_id]
  key_name                    = var.key_pair_name
  associate_public_ip_address = true

  # Require IMDSv2 tokens for instance metadata requests.
  metadata_options {
    http_endpoint               = "enabled"
    http_tokens                 = "required"
    http_put_response_hop_limit = 1
    instance_metadata_tags      = "enabled"
  }

  root_block_device {
    volume_size           = var.root_volume_size
    volume_type           = var.root_volume_type
    encrypted             = true
    delete_on_termination = true

    tags = {
      Name = "${var.name_tag}-root-volume"
    }
  }

  # Install a basic Apache/PHP runtime for the three small PHP sites.
  # Configure individual Apache virtual hosts after the domain names are known.
  user_data = <<-CLOUD_CONFIG
    #cloud-config
    preserve_hostname: true
    hostname: ${var.instance_name}
    timezone: ${var.timezone}
    package_update: true
    packages:
      - apache2
      - php
      - libapache2-mod-php
      - php-mysql
    runcmd:
      - [ systemctl, enable, --now, apache2 ]
  CLOUD_CONFIG

  tags = {
    Name = var.name_tag
    Role = "WebServer"
  }
}

resource "aws_eip" "web" {
  domain = "vpc"

  tags = {
    Name = "${var.name_tag}-eip"
  }
}

resource "aws_eip_association" "web" {
  allocation_id = aws_eip.web.id
  instance_id   = aws_instance.web.id
}
