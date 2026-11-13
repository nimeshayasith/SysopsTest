output "db_endpoint" {
  description = "RDS endpoint hostname and port."
  value       = aws_db_instance.this.endpoint
}

output "db_port" {
  description = "RDS MySQL port."
  value       = aws_db_instance.this.port
}

output "db_address" {
  description = "RDS endpoint hostname without the port."
  value       = aws_db_instance.this.address
}
