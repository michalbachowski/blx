/*Sequence pages_search_id */
create sequence pages_search_id_seq increment by 1 minvalue 1 no maxvalue start with 1 no cycle;

create table pages (
    url varchar primary key,
    realm varchar not null,
    title varchar not null,
    content text not null,
    metadata text,
    created timestamp without time zone not null default now(),
    search_id integer not null default nextval( 'pages_search_id_seq' ) unique
);

create table pages_trace (
    action char(1) not null,
    created timestamp without time zone not null default now(),
    dbuser text NOT NULL,
    constraint pages_trace_pkey primary key ( url, created ),
    like files
);
