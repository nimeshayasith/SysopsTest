variable "aws_region" {
  description = "AWS region for this deployment."
  type        = string
  default     = "ap-southeast-1"
}

variable "admin_ssh_cidr" {
  description = "Your current public IPv4 address in CIDR notation, for example 203.0.113.10/32."
  type        = string

  validation {
    condition     = can(cidrnetmask(var.admin_ssh_cidr)) && endswith(var.admin_ssh_cidr, "/32")
    error_message = "admin_ssh_cidr must be one public IPv4 address with a /32 mask, for example 203.0.113.10/32."
  }
}

variable "key_pair_name" {
  description = "Name of an existing EC2 key pair in this AWS region. Leave null if you will not SSH using a key pair."
  type        = string
  default     = null
}

variable "db_username" {
  description = "Master username for the MySQL RDS instance."
  type        = string
  default     = "sysopsadmin"
}

variable "db_password" {
  description = "Master password for the MySQL RDS instance. Supply through TF_VAR_db_password or an uncommitted tfvars file."
  type        = string
  sensitive   = true

  validation {
    condition     = length(var.db_password) >= 12 && length(var.db_password) <= 41
    error_message = "db_password must be between 12 and 41 characters for this MySQL RDS configuration."
  }
}

variable "db_name" {
  description = "Initial MySQL database name."
  type        = string
  default     = "sysopsdb"
}
