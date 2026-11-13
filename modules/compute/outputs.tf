output "instance_id" {
  description = "EC2 instance ID."
  value       = aws_instance.web.id
}

output "elastic_ip" {
  description = "Elastic IP associated with the web instance."
  value       = aws_eip.web.public_ip
}

output "instance_private_ip" {
  description = "Private IP address of the web instance."
  value       = aws_instance.web.private_ip
}
