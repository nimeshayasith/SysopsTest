variable "vpc_id" {
  description = "VPC in which to create security groups."
  type        = string
}

variable "admin_ssh_cidr" {
  description = "Administrator public IPv4 address in /32 CIDR notation."
  type        = string
}
