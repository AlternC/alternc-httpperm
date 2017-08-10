#!/bin/sh

# add this script to your IPTABLES after your standard matrix
# and before any final REJECT or LOG.
# this will allow HTTP and HTTPS OUTGOING connections only to allowed destinations
# and explicitely reject all others

# dependencies : 
# apt install iptables ipset

echo "http-perm START"
echo "creating ipsets"
ipset flush http-perm-ip4
ipset flush http-perm-net4
ipset flush http-perm-ip6
ipset flush http-perm-net6

ipset destroy http-perm-ip4
ipset destroy http-perm-net4
ipset destroy http-perm-ip6
ipset destroy http-perm-net6

ipset create http-perm-ip4  hash:ip  family inet maxelem 131072
ipset create http-perm-net4 hash:net family inet maxelem 65536
ipset create http-perm-ip6  hash:ip  family inet6 maxelem 131072
ipset create http-perm-net6 hash:net family inet6 maxelem 65536

echo "iptables for httpperm"
iptables -F httpperm  2>/dev/null
iptables -X httpperm 2>/dev/null
iptables -N httpperm
iptables -A httpperm -m set --match-set http-perm-ip4 dst -j ACCEPT
iptables -A httpperm -m set --match-set http-perm-net4 dst -j ACCEPT

iptables -A OUTPUT -p tcp -m multiport --dport 80,443 -d 127.0.0.1 --syn -j ACCEPT
iptables -A OUTPUT -p tcp -m multiport --dport 80,443 -j httpperm

echo "iptables for http6perm"
ip6tables -F httpperm  2>/dev/null
ip6tables -X httpperm 2>/dev/null
ip6tables -N httpperm
ip6tables -A httpperm -m set --match-set http-perm-ip6 dst -j ACCEPT
ip6tables -A httpperm -m set --match-set http-perm-net6 dst -j ACCEPT

ip6tables -A OUTPUT -p tcp -m multiport --dport 80,443 -d ::1 --syn -j ACCEPT
ip6tables -A OUTPUT -p tcp -m multiport --dport 80,443 -j httpperm

echo "http-perm END"

# LOGS HTTP and HTTPS : 
iptables  -A OUTPUT -p tcp -m multiport --dport 80,443 --syn -j NFLOG
ip6tables -A OUTPUT -p tcp -m multiport --dport 80,443 --syn -j NFLOG

# REJECTS (you may want to allow some BEFORE that script ;) ) 
iptables  -A OUTPUT -p tcp -m multiport --dport 80,443 --syn -j REJECT
ip6tables -A OUTPUT -p tcp -m multiport --dport 80,443 --syn -j REJECT
