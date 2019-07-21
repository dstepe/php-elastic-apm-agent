# php-elastic-apm-agent
Agent for the Elastic APM service implemented in PHP

## Development

Design goal: Ability to easily support to APM releases

Methods:

 - Use JSON schemas from APM Server
 - Generate low level classes for schema objects
 - Use schemas to validate produced JSON data
 - Abstract higher level classes from version specific
 - Use factories based on APM version (and schema version if needed)

### JSON Schemas

Clone https://github.com/elastic/apm-server

Switch to desired branch

Copy {apm-server}/docs/spec to {php-elastic-apm-agent}/apm-{version}/docs/spec

```bash
git checkout 6.7
cp -r ../apm-server/docs/spec schemas/apm-6.7/docs/spec
```
