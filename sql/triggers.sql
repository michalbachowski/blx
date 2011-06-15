create or replace function tr_pages_trace()
returns TRIGGER language 'plpgsql' security definer as $$
begin
    IF 'DELETE' = TG_OP THEN
        INSERT INTO pages_trace SELECT 'D', now(), user, OLD.*;
        RETURN OLD;
    ELSIF 'UPDATE' = TG_OP THEN
        INSERT INTO pages_trace SELECT 'U', now(), user, NEW.*;
        RETURN NEW;
    ELSIF 'INSERT' = TG_OP THEN
        INSERT INTO pages_trace SELECT 'I', now(), user, NEW.*;
        RETURN NEW;
    END IF;
    return null;
end;
$$;

CREATE TRIGGER trg_pages_trace
AFTER INSERT on pages
    FOR EACH ROW EXECUTE PROCEDURE tr_pages_trace();
