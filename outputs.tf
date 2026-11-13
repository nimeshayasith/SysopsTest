output "vpc_id" {
  description = "ID of the sysops VPC."
  value       = module.network.vpc_id
}

output "public_subnet_ids" {
  description = "IDs of the two public subnets."
  value       = module.network.public_subnet_ids
}

output "private_subnet_ids" {
  description = "IDs of the two private database subnets."
  value       = module.network.private_subnet_ids
}

output "web_instance_id" {
  description = "ID of the Ubuntu web EC2 instance."
  value       = module.compute.instance_id
}

output "web_elastic_ip" {
  description = "Elastic IP address associated with the web EC2 instance."
  value       = module.compute.elastic_ip
}

output "web_security_group_id" {
  description = "ID of the web security group."
  value       = module.security.web_security_group_id
}

output "db_endpoint" {
  description = "Private endpoint of the MySQL RDS instance."
  value       = module.database.db_endpoint
}

output "db_port" {
  description = "Port used by the MySQL RDS instance."
  value       = module.database.db_port
}
