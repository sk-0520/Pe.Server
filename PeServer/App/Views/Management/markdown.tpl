{extends file='default.tpl'}
{block name='TITLE'}Markdown{/block}
{block name='BODY'}

{markdown level='administrator' class='api'}
{literal}

# 見出し1
## 見出し2
### 見出し3
#### 見出し4
##### 見出し5
###### 見出し6

---

1. UL 1
1. UL 2
   1. UL 2-1
   1. UL 2-2
1. UL 3

---

* OL 1
* OL 2
   * OL 2-1
   * OL 2-2
* OL 3

---

dt 1
:   dd 1

dt 2
:   dd 2

---

*em*

---

**strong**

---

これは `inline code` だよ！

---

```
block code 1
block code 2
block code 3
```

---

```c
size_t strlen(const char* s)
{
	if(!s) {
		puts("DEBUG: NULL!");
		return 0;
	}

	size_t length = 0; // variable

	while(*s++) {
		/*
		increment
		*/
		length += 1;
	}

	return length;
}
```

(動くかは知らん)

---
| table  | left | center | right |
|--------|:-----|:------:|------:|
| TABLE  | LEFT | CENTER | RIGHT |
| EMPTY  |      | B<br>R |       |
| `CODE` | 12   | 34     | 56    |


---


---


---

{/literal}
{/markdown}


{/block}
