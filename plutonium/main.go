package main

import (
	"github.com/go-vgo/robotgo"
	hook "github.com/robotn/gohook"
	"math/rand"
	"time"
)

var activeAutoclicker = false
var clicking = false
var willClick = true
var down = false
var miliseconds time.Duration = 100

func main() {
	s := hook.Start()
	defer hook.End()

	go func() {
		for {
			if clicking && activeAutoclicker {
				willClick = false

				for i := 0; i < rand.Intn(3); i++ {
					click()
				}
				click()

				willClick = true
				time.Sleep(time.Millisecond * miliseconds)
			}
			time.Sleep(1 * time.Millisecond)
		}
	}()

	for {
		select {
		case i := <-s:
			if i.Kind > hook.KeyDown && i.Kind < hook.KeyUp {
				if i.Rawcode == 118 {
					activeAutoclicker = !activeAutoclicker
					clicking = false
					willClick = true
				}
			}
			if activeAutoclicker && willClick {
				if i.Button == 5 {
					if i.Kind == hook.MouseHold {
						clicking = true
					}
					if i.Kind == hook.MouseDown && !down {
						clicking = false
					}
				}
			}
		}
	}
}

func click() {
	down = true
	robotgo.MouseToggle("down", "left")
	robotgo.MouseToggle("up", "left")
	down = false
}
