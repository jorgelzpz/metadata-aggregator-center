CREATE TABLE sets (
      id INTEGER PRIMARY KEY,
      url VARCHAR(256) NOT NULL DEFAULT '',
      filter VARCHAR(255) NOT NULL DEFAULT '',
      xslt VARCHAR(255) NOT NULL DEFAULT 'tidy.xsl',
      name VARCHAR(100) NOT NULL DEFAULT '' UNIQUE,
      cacheduration VARCHAR(100) NOT NULL DEFAULT 'PT5H',
      validuntil VARCHAR(100) NOT NULL DEFAULT 'P10D'
);

CREATE TABLE entities (
      id INTEGER PRIMARY KEY,
      setid INTEGER,
      entityid VARCHAR(256) NOT NULL DEFAULT '',

      FOREIGN KEY(setid) REFERENCES sets(id)
);
