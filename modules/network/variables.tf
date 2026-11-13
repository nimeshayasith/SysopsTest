variable "vpc_name" {
  description = "Name tag for the VPC."
  type        = string
}

variable "vpc_cidr" {
  description = "CIDR block for the VPC."
  type        = string
}

variable "public_subnets" {
  description = "Map of public subnets keyed by a stable logical name."
  type = map(object({
    name              = string
    cidr              = string
    availability_zone = string
  }))
}

variable "private_subnets" {
  description = "Map of private subnets keyed by a stable logical name."
  type = map(object({
    name              = string
    cidr              = string
    availability_zone = string
  }))
}
