output "web_security_group_id" {
  description = "Security group ID for the web instance."
  value       = aws_security_group.web.id
}

output "db_security_group_id" {
  description = "Security group ID for the RDS instance."
  value       = aws_security_group.db.id
}
