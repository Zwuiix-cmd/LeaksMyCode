
# ServersStats

## API Reference

Server address: 45.145.167.106:19200

#### Bedrock Skin
| Request                | Response    | Description                               |
|:-----------------------|:------------|:------------------------------------------|
| `/api/skin/:user`      | `image/png` | `Get the skin of a player in our db`      |
| `/api/skin/:user/head` | `image/png` | `Get the head skin of a player in our db` |

#### Bedrock Stats
| Request              | Response | Description               |
|:---------------------|:---------|:--------------------------|
| `/api/stats/plugins` | `json`   | `Get plugin statistics.`  |
| `/api/stats/players` | `json`   | `Get players statistics.` |

#### Xbox
| Request                       | Response | Description                 |
|:------------------------------|:---------|:----------------------------|
| `/api/xbox/xuid/:gamertag`    | `string` | `Get xuid with gamertag.`   |
| `/api/xbox/profile/:gamertag` | `json`   | `Get gamertag information.` |

#### Query
| Request                     | Response | Description               |
|:----------------------------|:---------|:--------------------------|
| `/api/query/:address/:port` | `json`   | `Get server information.` |

#### Other request (HTML)
```http
http://45.145.167.106:19200/query/:address/:port
```

##### Exemple [Click](http://45.145.167.106:19200/query/linesia.eu/19132)
## Support
For support, join [Discord](https://discord.gg/musui) server.


## Authors

- [@Zwuiix-cmd](https://www.github.com/Zwuiix-cmd)
