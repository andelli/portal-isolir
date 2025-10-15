FROM internetsystemsconsortium/bind9:9.18
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]
CMD ["-g", "-u", "bind"]
