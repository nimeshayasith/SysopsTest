output "vpc_id" {
  description = "VPC ID."
  value       = aws_vpc.this.id
}

output "public_subnet_ids" {
  description = "Public subnet IDs keyed by logical name."
  value       = { for key, subnet in aws_subnet.public : key => subnet.id }
}

output "private_subnet_ids" {
  description = "Private subnet IDs keyed by logical name."
  value       = { for key, subnet in aws_subnet.private : key => subnet.id }
}

output "public_route_table_id" {
  description = "Public route table ID."
  value       = aws_route_table.public.id
}
