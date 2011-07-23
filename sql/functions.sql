create or replace function get_page( v_url_i varchar )
returns setof pages language 'plpgsql' security definer as $$
begin
    return query
        select
            *
        from
            blx.pages
        where
            url = v_url_i;
end;
$$;


create or replace function set_page(
    v_url_i varchar, v_realm_i varchar, v_title_i varchar, v_content_i text, v_metadata_i text default null )
returns boolean language 'plpgsql' security definer as $$
begin
    -- update page
    update
        pages
    set
        realm = v_realm_i,
        title = v_title_i,
        content = v_content_i,
        metadata = v_metadata_i
    where
        url = v_url_i;
    -- if updated - exit
    if found then
        return true;
    end if;
    -- add page
    insert into
        blx.pages
    (
        url,
        realm,
        title,
        content,
        metadata
    ) values (
        v_url_i,
        v_realm_i,
        v_title_i,
        v_content_i,
        v_metadata_i
    );
    return true;
end;
$$;
