variable "db_identifier" {
  description = "RDS DB instance identifier."
  type        = string
}

variable "db_name" {
  description = "Initial database name."
  type        = string
}

variable "db_username" {
  description = "Master username."
  type        = string
}

variable "db_password" {
  description = "Master password. Sensitive, but still stored in Terraform state."
  type        = string
  sensitive   = true
}

variable "db_subnet_ids" {
  description = "Private subnet IDs in at least two Availability Zones for the DB subnet group."
  type        = list(string)
}

variable "db_security_group_id" {
  description = "Database security group ID."
  type        = string
}

variable "instance_class" {
  description = "RDS instance class."
  type        = string
  default     = "db.t3.micro"
}

variable "allocated_storage" {
  description = "Allocated RDS storage in GiB."
  type        = number
  default     = 20
}

variable "multi_az" {
  description = "Whether to enable an RDS Multi-AZ deployment."
  type        = bool
  default     = false
}
