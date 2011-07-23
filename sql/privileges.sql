ALTER TABLE pages OWNER TO jaskinia;
ALTER TABLE pages_trace OWNER TO jaskinia;

GRANT USAGE ON SCHEMA blx TO jaskinia_common;

ALTER FUNCTION get_page(varchar) OWNER TO jaskinia;
REVOKE ALL ON FUNCTION get_page(varchar) FROM PUBLIC;
GRANT ALL ON FUNCTION get_page(varchar) TO jaskinia_common;

ALTER FUNCTION set_page(varchar, varchar, varchar, text, text) owner to jaskinia;
REVOKE ALL ON FUNCTION set_page(varchar, varchar, varchar, text, text) FROM PUBLIC;
GRANT ALL ON FUNCTION set_page(varchar, varchar, varchar, text, text) TO jaskinia_common;
