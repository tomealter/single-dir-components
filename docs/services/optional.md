# Additional optional resources

## Adding composer install upon starting a project

If you want to have Composer run `composer install` every time a project is started, add the following in the `.ddev/config.yaml`

```yaml
hooks:
  post-start:
    - exec: "composer install"
```
## Do you need to include any more branches for multidev?

If yes, that would be done in the `.buildkite/pipeline.yml`

```yaml
branches:
  - match: live
    target: master
  ...
  - match: [branch name on f1 github]
    target: [multi-dev name]
```
```yaml
branches:
  - live
  - main
  - integration
  - [branch name on f1 github]
  plugins:
  ...
```
