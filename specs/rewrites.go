package main

import (
	"encoding/csv"
	"fmt"
	"log"
	"net/http"
	"os"
	"strings"
	"time"
)

const (
	Endpoint = "https://gatling.cleverapps.io"
)

type Spec struct {
	Source string
	Target string
}

func readLines(path string) ([][]string, error) {
	f, err := os.Open(path)
	if err != nil {
		return nil, err
	}
	defer f.Close()

	lines, err := csv.NewReader(f).ReadAll()
	if err != nil {
		return nil, err
	}
	return lines, nil
}

func readSpec(lines [][]string) []Spec {
	var spec []Spec
	for _, line := range lines {
		if !strings.HasPrefix(line[0], "#") {
			spec = append(spec, Spec{Source: line[0], Target: line[1]})
		}
	}
	return spec
}

func checkSource(client *http.Client, source string, target string) error {
	req, err := http.NewRequest("GET", source, nil)
	if err != nil {
		return err
	}

	resp, err := client.Do(req)
	if err != nil {
		return err
	}

	if strings.HasSuffix(source, ".js") {
		if resp.StatusCode != 200 {
			return fmt.Errorf("Expected 200 but got %d", resp.StatusCode)
		}
	} else {
		if resp.StatusCode != 301 {
			return fmt.Errorf("Expected 301 but got %d", resp.StatusCode)
		}

		loc := resp.Header["Location"][0]
		if loc != target {
			return fmt.Errorf("Expected %s but got %s", target, loc)
		}
	}

	return nil
}

func checkTarget(client *http.Client, source string, target string) error {
	req, err := http.NewRequest("GET", target, nil)
	if err != nil {
		return err
	}

	resp, err := client.Do(req)
	if err != nil {
		return err
	}

	if resp.StatusCode != 200 {
		return fmt.Errorf("Expected 200 but got %d", resp.StatusCode)
	}

	return nil
}

func main() {
	lines, err := readLines("rewrites.csv")
	if err != nil {
		log.Fatal(err)
	}

	client := &http.Client{
		CheckRedirect: func(req *http.Request, via []*http.Request) error {
			return http.ErrUseLastResponse
		},
	}

	spec := readSpec(lines)
	for _, s := range spec {
		source := fmt.Sprintf("%s%s", Endpoint, s.Source)
		target := fmt.Sprintf("%s%s", Endpoint, s.Target)
		log.Printf("Checking %s over %s\n", source, target)

		err = checkSource(client, source, target)
		if err != nil {
			log.Fatal(err)
		} else {
			err = checkTarget(client, source, target)
			if err != nil {
				log.Fatal(err)
			}
		}

		time.Sleep(100 * time.Millisecond)
	}
}
