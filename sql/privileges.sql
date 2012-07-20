set search_path to blx;

ALTER TABLE pages OWNER TO jaskinia;
ALTER TABLE pages_trace OWNER TO jaskinia;

GRANT USAGE ON SCHEMA blx TO jaskinia_common;

ALTER FUNCTION list_pages(varchar) OWNER TO jaskinia;
REVOKE ALL ON FUNCTION list_pages(varchar) FROM PUBLIC;
REVOKE ALL ON FUNCTION list_pages(varchar) FROM jaskinia_common;
GRANT EXECUTE ON FUNCTION list_pages(varchar) TO jaskinia_common;

ALTER FUNCTION get_page(varchar, varchar) OWNER TO jaskinia;
REVOKE ALL ON FUNCTION get_page(varchar, varchar) FROM PUBLIC;
REVOKE ALL ON FUNCTION get_page(varchar, varchar) FROM jaskinia_common;
GRANT EXECUTE ON FUNCTION get_page(varchar, varchar) TO jaskinia_common;

ALTER FUNCTION set_page(varchar, varchar, varchar, integer,text, text) owner to jaskinia;
REVOKE ALL ON FUNCTION set_page(varchar, varchar, varchar, integer, text, text) FROM PUBLIC;
REVOKE ALL ON FUNCTION set_page(varchar, varchar, varchar, integer, text, text) FROM jaskinia_common;
GRANT EXECUTE ON FUNCTION set_page(varchar, varchar, varchar, integer, text, text) TO jaskinia_common;

alter sequence pages_search_id_seq owner to jaskinia;
revoke all on sequence pages_search_id_seq from public;
revoke all on sequence pages_search_id_seq from jaskinia_common;
grant select, update on sequence pages_search_id_seq to jaskinia_common;

alter function tr_pages_trace() owner to jaskinia;
revoke all on function tr_pages_trace() from public;
grant execute on function tr_pages_trace() to jaskinia_common;
