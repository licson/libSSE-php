CHANGELOG
===========

2.1.0
-------
 * Add Support of Symfony Http Foundation Compoent
 * Add PubSub Service
 * SSE use magic method instead of direct access
 * Add Redis and Memcahce Mechnism
 * Add SessionLike Mechnism
 
2.0.1
-------
 * Fix Order of arguments due to change of Utils::sseBlock
 
2.0.0
-------
 * Use namespaces directly
 * SSEEvent becomes interfaces \SSE\Event
 * rename SSEData as \SSE\Data
 * Add DataInterface for all Mechnism
 * SSEUtils rename as \SSE\Utils
 * All snake case method name rename to camel case
 * Change order of argument of Utils::sseBlock
  
1.0.0
-------
 * Add to Packagist as tonyhhtip/sse