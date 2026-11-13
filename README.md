# SysOpsTest - Terraform AWS infrastructure

This project creates the AWS infrastructure for three small PHP sites on one Ubuntu web server and a private MySQL RDS instance.

## Architecture

- Region: `ap-southeast-1`
- A dedicated VPC: `10.10.0.0/16` (does not use the default VPC)
- Public subnet A: `10.10.1.0/24` in `ap-southeast-1a`
- Public subnet B: `10.10.2.0/24` in `ap-southeast-1b`
- Private subnet A: `10.10.11.0/24` in `ap-southeast-1a`
- Private subnet B: `10.10.12.0/24` in `ap-southeast-1b`
- One Internet Gateway and one public route table with `0.0.0.0/0` to the IGW
- No NAT gateway; private subnets have no internet default route
- `web-sg`: inbound TCP 80/443 from anywhere and TCP 22 from the administrator's `/32` only
- `db-sg`: inbound TCP 3306 only from `web-sg`
- Ubuntu 24.04 LTS `t3.micro` EC2 instance in public subnet A
- Encrypted 20 GiB `gp3` EC2 root volume and an Elastic IP
- IMDSv2 is required
- Basic Apache and PHP packages are installed by cloud-init; configure the three Apache virtual hosts when the site domain names are available
- MySQL RDS `db.t3.micro`, private, encrypted, and `multi_az = false`

## Subnet assumption

The supplied requirements called `10.10.2.0/24` a private subnet, but also said it should auto-assign public IPv4 addresses and that the public route table must be associated with both public subnets. This configuration treats `10.10.2.0/24` as **Public Subnet B**. The two `10.10.11.0/24` and `10.10.12.0/24` subnets are the private RDS subnets.

RDS needs a DB subnet group spanning at least two Availability Zones. That does **not** make the DB Multi-AZ; this project explicitly sets `multi_az = false`.

## Files and module separation

```text
.
├── versions.tf
├── variables.tf
├── main.tf
├── outputs.tf
├── terraform.tfvars.example
└── modules/
    ├── network/
    ├── security/
    ├── compute/
    └── database/
```

Provider `default_tags` applies `Project = SysOpsTest`, `Owner = Nimesha Yasith`, and `ManagedBy = Terraform` to taggable resources. Resource-specific `Name` tags are also set. AWS resource types that do not support tags (such as route-table associations) cannot be tagged.

## Prerequisites

1. Terraform CLI 1.6 or newer.
2. AWS CLI credentials configured for an IAM identity with permission to create VPC, EC2, EIP, security group, IAM/SSM read (for the public SSM AMI parameter), and RDS resources in `ap-southeast-1`.
3. An EC2 key pair already created in `ap-southeast-1` if you want to SSH with a downloaded private key.
4. Your current public IPv4 address for the SSH allow-list.

Find your current public IPv4 address from Git Bash / terminal:

```bash
curl -4 https://checkip.amazonaws.com
```

Add `/32` to that address, for example `203.0.113.10/32` (example only).

## Configure and deploy

From the project root:

```bash
cp terraform.tfvars.example terraform.tfvars
```

Edit `terraform.tfvars` and set `admin_ssh_cidr` and (optionally) `key_pair_name`. Do not commit the real `terraform.tfvars` file.

Set the RDS password as an environment variable so it is not written in the tfvars file. In Git Bash:

```bash
export TF_VAR_db_password='Choose-A-Strong-Password-Here'
```

Then run:

```bash
terraform init
terraform fmt -recursive
terraform validate
terraform plan
terraform apply
```

Terraform will display the Elastic IP and private RDS endpoint after deployment. The RDS endpoint is private and should be accessed by the web instance, not directly from your laptop over the public internet.

## Cleanup

This is a lab/test configuration. RDS backups are disabled, deletion protection is disabled, and the final snapshot is skipped to reduce test cost and simplify cleanup. This is not a recommended production backup policy.

When the test is finished:

```bash
terraform destroy
```

Keep your Terraform state secure. Terraform marks the DB password as sensitive in output, but the password is still stored in the state file. Do not commit state files or real credentials to Git. For production, use a remote encrypted backend with restricted access and a managed secrets workflow.
