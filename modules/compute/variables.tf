variable "ami_id" {
  description = "Ubuntu 24.04 AMI ID for the selected region."
  type        = string
}

variable "subnet_id" {
  description = "Public subnet in which to launch the web instance."
  type        = string
}

variable "web_security_group_id" {
  description = "Web security group ID."
  type        = string
}

variable "key_pair_name" {
  description = "Existing EC2 key pair name, or null."
  type        = string
  default     = null
}

variable "instance_name" {
  description = "EC2 instance Name tag and hostname."
  type        = string
}

variable "name_tag" {
  description = "Name tag prefix for the instance, volume and EIP (must start with sysops-<firstname>-)."
  type        = string
}

variable "instance_type" {
  description = "EC2 instance type."
  type        = string
  default     = "t3.micro"
}

variable "root_volume_size" {
  description = "Root EBS volume size in GiB."
  type        = number
  default     = 20
}

variable "root_volume_type" {
  description = "Root EBS volume type."
  type        = string
  default     = "gp3"
}

variable "timezone" {
  description = "Linux timezone configured during cloud-init."
  type        = string
  default     = "Asia/Colombo"
}
