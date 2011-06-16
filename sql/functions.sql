create or replace function set_page(
    v_url_i varchar, v_title_i varchar, v_content_i text, v_metadata_i text default null )
returns boolean language 'plpgsql' as $$
begin
    -- update page
    update
        pages
    set
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
        pages
    (
        url,
        title,
        content,
        metadata
    ) values (
        v_url_i,
        v_title_i,
        v_content_i,
        v_metadata_i
    );
    return true;
end;
$$;
