CREATE TABLE rssfeed (
 ArticleID CHAR(8) UNIQUE,
 Title VARCHAR(256),
 Category VARCHAR(16),
 
 Strings1 VARCHAR(256),
 Strings2 VARCHAR(256),
 Strings3 VARCHAR(256),
 
 Url text,
 Time datetime,
 LastUpdated datetime
);