CREATE TABLE tasks (
    id integer NOT NULL,
    text character varying(255),
    priority numeric(1,0),
    due_date date,
    completed boolean
);


CREATE SEQUENCE tasks_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE tasks_id_seq OWNED BY tasks.id;

CREATE TABLE users (
    id integer NOT NULL,
    name character varying(255),
    login character varying(255),
    password character varying(255)
);


CREATE SEQUENCE users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE users_id_seq OWNED BY users.id;

ALTER TABLE ONLY tasks ALTER COLUMN id SET DEFAULT nextval('tasks_id_seq'::regclass);
ALTER TABLE ONLY users ALTER COLUMN id SET DEFAULT nextval('users_id_seq'::regclass);

SELECT pg_catalog.setval('tasks_id_seq', 1, true);
SELECT pg_catalog.setval('users_id_seq', 1, true);

ALTER TABLE ONLY tasks
    ADD CONSTRAINT id_pk PRIMARY KEY (id);

ALTER TABLE ONLY users
    ADD CONSTRAINT user_pk PRIMARY KEY (id);

ALTER TABLE tasks 
    ADD COLUMN user_id integer;

ALTER TABLE tasks 
    ADD CONSTRAINT tasks_users_fk FOREIGN KEY (user_id) REFERENCES users (id)
    ON UPDATE NO ACTION ON DELETE CASCADE;

CREATE INDEX fki_tasks_users_fk ON tasks(user_id);