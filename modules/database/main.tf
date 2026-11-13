resource "aws_db_subnet_group" "this" {
  name        = "${var.db_identifier}-subnet-group"
  description = "Private subnets for ${var.db_identifier}"
  subnet_ids  = var.db_subnet_ids

  tags = {
    Name = "${var.db_identifier}-subnet-group"
    Tier = "Private"
  }
}

resource "aws_db_instance" "this" {
  identifier        = var.db_identifier
  engine            = "mysql"
  instance_class    = var.instance_class
  allocated_storage = var.allocated_storage
  storage_type      = "gp3"
  storage_encrypted = true

  db_name  = var.db_name
  username = var.db_username
  password = var.db_password
  port     = 3306

  db_subnet_group_name   = aws_db_subnet_group.this.name
  vpc_security_group_ids = [var.db_security_group_id]
  publicly_accessible    = false
  multi_az               = var.multi_az

  # Test/lab settings to keep the deployment inexpensive and easy to remove.
  backup_retention_period = 0
  deletion_protection     = false
  skip_final_snapshot     = true

  tags = {
    Name = var.db_identifier
    Tier = "Database"
  }
}
